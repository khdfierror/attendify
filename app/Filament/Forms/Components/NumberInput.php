<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\RawJs;

class NumberInput extends TextInput
{
    protected mixed $precision = 2;

    public function precision(mixed $precision): static
    {
        $this->precision = $precision;

        return $this;
    }

    public function getPrecision($value): int
    {
        return intval($this->precision);
    }

    protected function setUp(): void
    {
        $this->mask(RawJs::make(<<<'JS'
                $money($input)
            JS))
            ->stripCharacters(',')
            ->formatStateUsing(fn (?string $state) => number_format($state, $this->getPrecision($state)))
            ->beforeStateDehydrated(function (Get $get, Set $set) {
                $name = $this->getName();
                $value = floatval(preg_replace('/[^0-9.]/', '', $get($name)));
                $set($name, $value);
            });
    }
}
