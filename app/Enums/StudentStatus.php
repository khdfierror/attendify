<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StudentStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case GRADUATED = 'graduated';
    case TRANSFERRED = 'transferred';
    case DROPPED_OUT = 'dropped_out';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::GRADUATED => 'Lulus',
            self::TRANSFERRED => 'Dipindahkan',
            self::DROPPED_OUT => 'Keluar',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            default => null,
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::GRADUATED => 'succes',
            self::TRANSFERRED => 'primary',
            self::DROPPED_OUT => 'gray',
        };
    }
}
