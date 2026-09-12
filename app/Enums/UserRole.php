<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Voter = 'voter';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Voter => 'Pemilih (Voter)',
        };
    }
}
