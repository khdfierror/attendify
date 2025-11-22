<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Kelas: int implements HasLabel
{
    case VII = 7;
    case VIII = 8;
    case IX = 9;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VII => 'Kelas 7',
            self::VIII => 'Kelas 8',
            self::IX => 'Kelas 9'
        };
    }
}
