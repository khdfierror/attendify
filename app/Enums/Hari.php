<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Hari: string implements HasLabel
{
    case SENIN = 'senin';
    case SELASA = 'selasa';
    case RABU = 'rabu';
    case KAMIS = 'kamis';
    case JUMAT = 'jumat';
    case SABTU = 'sabtu';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SENIN => 'Senin',
            self::SELASA => 'Selasa',
            self::RABU => 'Rabu',
            self::KAMIS => 'Kamis',
            self::JUMAT => 'Jumat',
            self::SABTU => 'Sabtu',
        };
    }
}
