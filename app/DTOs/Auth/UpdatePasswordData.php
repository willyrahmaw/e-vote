<?php

namespace App\DTOs\Auth;

final readonly class UpdatePasswordData
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currentPassword: (string) ($data['current_password'] ?? ''),
            newPassword: (string) ($data['password'] ?? $data['new_password'] ?? ''),
        );
    }
}
