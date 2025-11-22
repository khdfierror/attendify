<?php

namespace App\Filament\Resources;

use App\Enums\Hari;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Pelajaran';

    protected static ?string $modelLabel = 'Jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jadwal')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('class', 'name', fn (Builder $query) => $query->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name', fn (Builder $query) => $query->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('teacher_id')
                            ->label('Guru Pengampu')
                            ->relationship('teacher', 'name', function ($query) {
                                return $query->whereHas('roles', function ($q) {
                                    $q->where('name', 'guru');
                                });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('day')
                            ->label('Hari')
                            ->options(Hari::class)
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->required()
                            ->seconds(false)
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->required()
                            ->seconds(false)
                            ->after('start_time')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('room')
                            ->label('Ruang Kelas')
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('day')
                    ->label('Hari')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room')
                    ->label('Ruang')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::where('is_active', true)->first()?->id),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('day')
                    ->label('Hari')
                    ->options([
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Guru')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->default(true),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplikat')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->action(function (Schedule $record) {
                            $newSchedule = $record->replicate();
                            $newSchedule->save();
                        })
                        ->requiresConfirmation(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
