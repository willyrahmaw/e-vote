<?php

namespace App\Enums;

enum ResultVisibility: string
{
    case Hidden = 'hidden';
    case AfterVote = 'after_vote';
    case AfterElection = 'after_election';
    case Live = 'live';

    public function label(): string
    {
        return match ($this) {
            self::Hidden => 'Disembunyikan (Hanya Admin)',
            self::AfterVote => 'Setelah Menggunakan Hak Suara',
            self::AfterElection => 'Setelah Pemilihan Berakhir',
            self::Live => 'Realtime / Publik Terbuka',
        };
    }
}
