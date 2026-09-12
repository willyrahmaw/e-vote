<?php

namespace App\Livewire\Admin;

use App\Actions\Setting\UpdateWebsiteSettingsAction;
use App\DTOs\Setting\UpdateWebsiteSettingsData;
use App\Models\User;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Pengaturan Website')]
class WebsiteSetting extends Component
{
    use WithFileUploads;

    public string $activeTab = 'branding';

    // Branding & Identity
    public string $app_name = '';
    public string $institution_name = '';
    public string $app_tagline = '';
    public string $app_description = '';
    public string $footer_copyright = '';
    public ?string $app_logo = null;
    public ?string $app_favicon = null;

    // File Uploads
    public $logoUpload = null;
    public $faviconUpload = null;

    // Contact & Support
    public string $contact_email = '';
    public string $contact_phone = '';
    public string $institution_address = '';

    // Features & Security Preferences
    public bool $enable_public_monitor = true;
    public bool $enable_ballot_verification = true;
    public string $announcement_banner = '';

    protected function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:150'],
            'app_tagline' => ['nullable', 'string', 'max:255'],
            'app_description' => ['nullable', 'string', 'max:1000'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'logoUpload' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'faviconUpload' => ['nullable', 'file', 'mimes:ico,png,svg,jpg,jpeg', 'max:1024'],
            'contact_email' => ['nullable', 'email', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'institution_address' => ['nullable', 'string', 'max:500'],
            'enable_public_monitor' => ['boolean'],
            'enable_ballot_verification' => ['boolean'],
            'announcement_banner' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'app_name.required' => 'Nama aplikasi wajib diisi.',
            'institution_name.required' => 'Nama institusi / organisasi penyelenggara wajib diisi.',
            'contact_email.email' => 'Format email kontak tidak valid.',
            'logoUpload.image' => 'File logo harus berupa gambar (PNG, JPG, SVG, WebP).',
            'logoUpload.max' => 'Ukuran file logo maksimal 2MB.',
            'faviconUpload.max' => 'Ukuran favicon maksimal 1MB.',
        ];
    }

    public function mount(SettingService $service): void
    {
        $settings = $service->getAll();

        $this->app_name = $settings['app_name'] ?? 'E-Voting Terpadu';
        $this->institution_name = $settings['institution_name'] ?? '';
        $this->app_tagline = $settings['app_tagline'] ?? '';
        $this->app_description = $settings['app_description'] ?? '';
        $this->footer_copyright = $settings['footer_copyright'] ?? '';
        $this->app_logo = $settings['app_logo'] ?? null;
        $this->app_favicon = $settings['app_favicon'] ?? null;

        $this->contact_email = $settings['contact_email'] ?? '';
        $this->contact_phone = $settings['contact_phone'] ?? '';
        $this->institution_address = $settings['institution_address'] ?? '';

        $this->enable_public_monitor = (bool) ($settings['enable_public_monitor'] ?? true);
        $this->enable_ballot_verification = (bool) ($settings['enable_ballot_verification'] ?? true);
        $this->announcement_banner = $settings['announcement_banner'] ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['branding', 'contact', 'features']) ? $tab : 'branding';
    }

    public function deleteLogo(SettingService $service): void
    {
        if ($this->app_logo) {
            $service->deleteImage($this->app_logo);
            $service->updateMany(['app_logo' => '']);
            $this->app_logo = null;
        }
        $this->logoUpload = null;
        $this->dispatch('swal:success', message: 'Logo brand berhasil dihapus.');
    }

    public function deleteFavicon(SettingService $service): void
    {
        if ($this->app_favicon) {
            $service->deleteImage($this->app_favicon);
            $service->updateMany(['app_favicon' => '']);
            $this->app_favicon = null;
        }
        $this->faviconUpload = null;
        $this->dispatch('swal:success', message: 'Favicon berhasil dihapus.');
    }

    public function save(UpdateWebsiteSettingsAction $action, SettingService $settingService): void
    {
        $this->validate();

        /** @var User $admin */
        $admin = Auth::user();

        try {
            if ($this->logoUpload) {
                if ($this->app_logo) {
                    $settingService->deleteImage($this->app_logo);
                }
                $this->app_logo = $settingService->uploadImage($this->logoUpload, 'settings');
                $this->logoUpload = null;
            }

            if ($this->faviconUpload) {
                if ($this->app_favicon) {
                    $settingService->deleteImage($this->app_favicon);
                }
                $this->app_favicon = $settingService->uploadImage($this->faviconUpload, 'settings');
                $this->faviconUpload = null;
            }

            $dto = UpdateWebsiteSettingsData::fromArray([
                'app_name' => $this->app_name,
                'institution_name' => $this->institution_name,
                'app_logo' => $this->app_logo,
                'app_favicon' => $this->app_favicon,
                'app_tagline' => $this->app_tagline,
                'app_description' => $this->app_description,
                'footer_copyright' => $this->footer_copyright,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'institution_address' => $this->institution_address,
                'enable_public_monitor' => $this->enable_public_monitor,
                'enable_ballot_verification' => $this->enable_ballot_verification,
                'announcement_banner' => $this->announcement_banner,
            ]);

            $action->execute($admin, $dto);

            $this->dispatch('swal:success', message: 'Pengaturan website berhasil disimpan!');
            session()->flash('status', 'Pengaturan website berhasil diperbarui.');
        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.admin.website-setting');
    }
}
