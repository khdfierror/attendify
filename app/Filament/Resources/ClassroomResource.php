<?php

namespace App\Filament\Resources;

use App\Enums\Kelas;
use App\Filament\Resources\ClassroomResource\Pages;
use App\Models\AcademicYear;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Data Kelas';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('X-A, XI-IPA-1, XII-IPS-2')
                    ->required()
                    ->maxLength(50),

                Forms\Components\Select::make('grade_level')
                    ->label('Tingkat')
                    ->options(Kelas::class)
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name', fn (Builder $query) => $query->where('is_active', true)
                    )
                    ->searchable()
                    ->preload()
                    ->helperText('Kosongkan untuk kelas 10 (belum ada penjurusan)'),

                Forms\Components\Select::make('homeroom_teacher_id')
                    ->label('Wali Kelas')
                    ->relationship('homeroomTeacher', 'name')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('max_students')
                    ->label('Kapasitas Maksimal')
                    ->numeric()
                    ->default(40)
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('siswa'),

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
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('grade_level')
                    ->label('Tingkat')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('major.name')
                    ->label('Jurusan')
                    ->badge()
                    ->color('warning')
                    ->default('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('homeroomTeacher.name')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->badge()
                    ->color(fn (ClassRoom $record) => $record->students_count >= $record->max_students ? 'danger' : 'success'
                    )
                    ->formatStateUsing(fn (ClassRoom $record, $state) => "$state / {$record->max_students}"
                    )
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYear::where('is_active', true)->first()?->id),

                Tables\Filters\SelectFilter::make('grade_level')
                    ->label('Tingkat')
                    ->options([
                        10 => 'Kelas 10 (X)',
                        11 => 'Kelas 11 (XI)',
                        12 => 'Kelas 12 (XII)',
                    ]),

                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('homeroom_teacher_id')
                    ->label('Wali Kelas')
                    ->relationship('homeroomTeacher', 'name')
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
                    Tables\Actions\Action::make('view_students')
                        ->label('Lihat Siswa')
                        ->icon('heroicon-o-users')
                        ->color('info')
                        ->url(fn (Classroom $record): string => route('filament.admin.resources.students.index', [
                            'tableFilters' => ['class_id' => ['value' => $record->id]],
                        ])
                        ),

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

                ]),
            ])
            ->defaultSort('grade_level', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClassrooms::route('/'),
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
        return static::getModel()::where('is_active', true)->count();
    }
}
