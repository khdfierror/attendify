<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?string $modelLabel = 'Tahun Ajaran';

    protected static ?string $pluralModelLabel = 'Data Tahun Ajaran';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Tahun Ajaran')
                    ->placeholder('2024/2025')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('start_year')
                    ->label('Tahun Mulai')
                    ->numeric()
                    ->required()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->default(now()->year),

                Forms\Components\TextInput::make('end_year')
                    ->label('Tahun Selesai')
                    ->numeric()
                    ->required()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->default(now()->year + 1),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->displayFormat('d/m/Y')
                    ->default(now()->startOfYear()->addMonths(6)),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->native(false)
                    ->suffixIcon('heroicon-o-calendar')
                    ->displayFormat('d/m/Y')
                    ->after('start_date')
                    ->default(now()->endOfYear()->addMonths(6)),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                Forms\Components\Toggle::make('is_active')
                    ->label('Tahun Ajaran Aktif')
                    ->helperText('Hanya boleh ada 1 tahun ajaran aktif. Mengaktifkan ini akan menonaktifkan tahun ajaran lainnya.')
                    ->default(false),

            ])
            ->inlineLabel()
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('start_year')
                    ->label('Tahun Mulai')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('end_year')
                    ->label('Tahun Selesai')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('classes_count')
                    ->label('Jumlah Kelas')
                    ->counts('classes')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                Tables\Filters\TrashedFilter::make(),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Tahun Ajaran')
                        ->modalDescription('Mengaktifkan tahun ajaran ini akan menonaktifkan tahun ajaran lainnya.')
                        ->action(function (AcademicYear $record) {
                            $record->update(['is_active' => true]);
                            \Filament\Notifications\Notification::make()
                                ->title('Tahun ajaran berhasil diaktifkan.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (AcademicYear $record) => ! $record->is_active),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAcademicYears::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->first();

        return $active ? $active->name : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
