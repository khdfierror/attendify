<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Absensi')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('bulk_attendance')
                ->label('Absensi Massal')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url(route('filament.admin.resources.attendances.bulk')),
        ];
    }
}
