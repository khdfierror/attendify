<?php

namespace App\Filament\Resources\Pengaturan\UserResource\Pages;

use App\Filament\Resources\Pengaturan\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (is_null($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
