<?php

namespace App\Livewire\Profile;

use App\Actions\Auth\UpdatePasswordAction;
use App\DTOs\Auth\UpdatePasswordData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class ProfileIndex extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
            'password_confirmation.required' => 'Konfirmasi kata sandi baru wajib diisi.',
        ];
    }

    public function updatePassword(UpdatePasswordAction $action): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        try {
            $dto = UpdatePasswordData::fromArray([
                'current_password' => $this->current_password,
                'password' => $this->password,
            ]);

            $action->execute($user, $dto);

            $this->reset(['current_password', 'password', 'password_confirmation']);

            $this->dispatch('swal:success', message: 'Kata sandi berhasil diperbarui! Keamanan akun Anda kini lebih terjaga.');
            session()->flash('status', 'Kata sandi berhasil diperbarui!');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($field, $msg);
                }
            }
            $this->dispatch('swal:error', message: $e->getMessage());
        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: 'Gagal memperbarui kata sandi: ' . $e->getMessage());
        }
    }

    public function render(): mixed
    {
        /** @var User $user */
        $user = Auth::user();
        $layout = $user->isAdmin() ? 'layouts.admin' : 'layouts.voter';

        /** @var mixed $view */
        $view = view('livewire.profile.profile-index', [
            'user' => $user,
        ]);

        return $view->layout($layout, ['title' => 'Profil & Keamanan Akun']);
    }
}

