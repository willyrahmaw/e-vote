<?php

namespace App\DTOs\Voter;

final readonly class ImportVoterData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $identifier = null,
        public ?string $password = null,
        public bool $isEligible = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name'] ?? ''),
            email: strtolower(trim($data['email'] ?? '')),
            identifier: isset($data['identifier']) && trim($data['identifier']) !== '' ? trim($data['identifier']) : null,
            password: isset($data['password']) && trim($data['password']) !== '' ? trim($data['password']) : null,
            isEligible: isset($data['is_eligible']) ? (bool) $data['is_eligible'] : true,
        );
    }
}
