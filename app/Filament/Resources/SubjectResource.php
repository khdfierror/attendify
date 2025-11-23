<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Mata Pelajaran';

    protected static ?string $modelLabel = 'Mata Pelajaran';

    protected static ?string $pluralModelLabel = 'Data Mata Pelajaran';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'data/mata-pelajaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Mata Pelajaran')
                    ->placeholder('MTK, FIS, BIO')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->alphaDash(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->placeholder('Matematika, Fisika, Biologi')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('credit_hours')
                    ->label('Jam Pelajaran per Minggu')
                    ->numeric()
                    ->default(2)
                    ->minValue(1)
                    ->maxValue(10)
                    ->suffix('jam')
                    ->helperText('Jumlah jam pelajaran dalam 1 minggu'),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Deskripsi singkat tentang mata pelajaran ini...')
                    ->rows(4),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

            ])
            ->inlineLabel()
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                Tables\Columns\TextColumn::make('credit_hours')
                    ->label('Jam/Minggu')
                    ->suffix(' jam')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('Jumlah Jadwal')
                    ->counts('schedules')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('Total Absensi')
                    ->counts('attendances')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->default(true),

                Tables\Filters\Filter::make('credit_hours')
                    ->form([
                        Forms\Components\TextInput::make('min_hours')
                            ->label('Min Jam')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_hours')
                            ->label('Max Jam')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_hours'],
                                fn (Builder $query, $hours): Builder => $query->where('credit_hours', '>=', $hours),
                            )
                            ->when(
                                $data['max_hours'],
                                fn (Builder $query, $hours): Builder => $query->where('credit_hours', '<=', $hours),
                            );
                    }),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_schedules')
                        ->label('Lihat Jadwal')
                        ->icon('heroicon-o-calendar-days')
                        ->color('info')
                        ->url(fn (Subject $record): string => route('filament.admin.resources.schedules.index', [
                            'tableFilters' => ['subject_id' => ['value' => $record->id]],
                        ])
                        )
                        ->visible(fn (Subject $record): bool => $record->schedules_count > 0),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),

                    Tables\Actions\BulkAction::make('update_credit_hours')
                        ->label('Update Jam Pelajaran')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('credit_hours')
                                ->label('Jam Pelajaran per Minggu')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(10),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['credit_hours' => $data['credit_hours']]);
                        })
                        ->requiresConfirmation(),

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }
}
