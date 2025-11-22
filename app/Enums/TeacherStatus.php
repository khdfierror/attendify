<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TeacherStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case RETIRED = 'retired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::RETIRED => 'Pensiun',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            default => null,
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::RETIRED => 'gray',
        };
    }
}
