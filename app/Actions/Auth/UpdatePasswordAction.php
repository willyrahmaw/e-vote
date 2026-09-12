<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdatePasswordData;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdatePasswordAction
{
    public function __construct(
        private AuditLogService $auditService,
    ) {}

    /**
     * Update user password securely and record audit log.
     *
     * @throws ValidationException
     */
    public function execute(User $user, UpdatePasswordData $data): void
    {
        if (! Hash::check($data->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            ]);
        }

        DB::transaction(function () use ($user, $data) {
            $user->update([
                'password' => Hash::make($data->newPassword),
                'password_changed_at' => now(),
            ]);

            $this->auditService->log(
                action: 'USER_UPDATE_PASSWORD',
                user: $user,
                model: $user,
                metadata: [
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'ip' => request()->ip(),
                ]
            );
        });
    }
}
