<?php

namespace App\Enums;

enum ElectionStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Ended = 'ended';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Terjadwal',
            self::Active => 'Berlangsung (Aktif)',
            self::Ended => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 border border-slate-200/80',
            self::Scheduled => 'bg-amber-50 text-amber-800 border border-amber-200/80',
            self::Active => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
            self::Ended => 'bg-blue-50 text-blue-800 border border-blue-200/80',
            self::Cancelled => 'bg-rose-50 text-rose-800 border border-rose-200/80',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-400',
            self::Scheduled => 'bg-amber-500',
            self::Active => 'bg-emerald-500 animate-pulse',
            self::Ended => 'bg-blue-500',
            self::Cancelled => 'bg-rose-500',
        };
    }
}
