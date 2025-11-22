<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasColor, HasLabel
{
    case PRESENT = 'present';
    case LATE = 'late';
    case SICK = 'sick';
    case PERMISSION = 'permission';
    case ABSENT = 'absent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRESENT => 'Hadir',
            self::LATE => 'Terlambat',
            self::SICK => 'Sakit',
            self::PERMISSION => 'Izin',
            self::ABSENT => 'Alpa',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            default => null,
            self::PRESENT => 'success',
            self::LATE => 'warning',
            self::SICK => 'primary',
            self::PERMISSION => 'primary',
            self::ABSENT => 'danger',
        };
    }
}
