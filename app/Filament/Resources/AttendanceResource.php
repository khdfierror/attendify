<?php

namespace App\Filament\Resources;

use App\Enums\AttendanceStatus;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\AcademicYear;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Data Absensi';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Absensi')
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('class', 'name', fn (Builder $query) => $query->where('is_active', true)
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('student_id', null))
                            ->columnSpan(1),

                        Forms\Components\Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name', function (Builder $query, callable $get) {
                                $classId = $get('class_id');
                                if ($classId) {
                                    return $query->where('class_id', $classId)->where('status', 'active');
                                }

                                return $query->where('status', 'active');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nis} - {$record->full_name}")
                            ->columnSpan(2),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Jam Masuk')
                            ->seconds(false)
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('check_out_time')
                            ->label('Jam Keluar')
                            ->seconds(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status Kehadiran')
                            ->options(AttendanceStatus::class)
                            ->required()
                            ->default('present')
                            ->native(false)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpan(2),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('Lampiran (Surat Keterangan)')
                            ->directory('attendances/attachments')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(2048)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('attachment')
                    ->label('Lampiran')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dicatat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Siswa')
                    ->relationship('student', 'full_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(AttendanceStatus::class)
                    ->multiple(),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options(AttendanceStatus::class)
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->requiresConfirmation(),

                ]),
            ])
            ->defaultSort('date', 'desc')
            ->defaultSort('check_in_time', 'desc');
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
            'bulk' => Pages\BulkAttendance::route('/bulk'),
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
        return static::getModel()::whereDate('date', today())->count();
    }
}
