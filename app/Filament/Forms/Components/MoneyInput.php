<?php

namespace App\Filament\Forms\Components;

class MoneyInput extends NumberInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix('Rp ')
            ->suffix(',-')
            ->extraInputAttributes([
                'class' => 'text-right',
            ]);
    }
}
