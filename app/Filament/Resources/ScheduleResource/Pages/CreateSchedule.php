<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function afterCreate(): void
    {
        // Check for conflicts
        if ($this->record->hasConflict()) {
            Notification::make()
                ->warning()
                ->title('Peringatan Konflik Jadwal')
                ->body('Terdapat jadwal lain yang bentrok dengan jadwal ini.')
                ->send();
        }
    }
}
