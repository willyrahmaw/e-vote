<?php

namespace App\Enums;

enum IndonesianTimezone: string
{
    case WIB = 'Asia/Jakarta';
    case WITA = 'Asia/Makassar';
    case WIT = 'Asia/Jayapura';

    public function label(): string
    {
        return match ($this) {
            self::WIB => 'WIB - Waktu Indonesia Barat (UTC+7 / Jakarta, Sumatera, Jawa, Pontianak)',
            self::WITA => 'WITA - Waktu Indonesia Tengah (UTC+8 / Bali, NTT, NTB, Sulawesi, Kalsel, Kaltim)',
            self::WIT => 'WIT - Waktu Indonesia Timur (UTC+9 / Maluku, Papua)',
        };
    }

    public function abbr(): string
    {
        return match ($this) {
            self::WIB => 'WIB',
            self::WITA => 'WITA',
            self::WIT => 'WIT',
        };
    }

    public static function currentAbbr(): string
    {
        $tz = config('app.timezone', 'Asia/Jakarta');
        return match ($tz) {
            'Asia/Jakarta' => 'WIB',
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB',
        };
    }

    public static function options(): array
    {
        return [
            self::WIB->value => self::WIB->label(),
            self::WITA->value => self::WITA->label(),
            self::WIT->value => self::WIT->label(),
        ];
    }
}
