<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendancePermissionResource\Pages;
use App\Models\AttendancePermission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancePermissionResource extends Resource
{
    protected static ?string $model = AttendancePermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Surat Izin';

    protected static ?string $modelLabel = 'Surat Izin';

    protected static ?string $pluralModelLabel = 'Pengajuan Surat Izin';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'akademik/surat-izin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Surat Izin')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name', function (Builder $query) {
                                return $query->where('status', 'active');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nis} - {$record->full_name}")
                            ->columnSpan(2),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Izin')
                            ->options([
                                'sick' => 'Sakit',
                                'permission' => 'Izin',
                                'other' => 'Lainnya',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('start_date')
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('duration')
                            ->label('Durasi')
                            ->content(function (callable $get) {
                                $start = $get('start_date');
                                $end = $get('end_date');

                                if ($start && $end) {
                                    $startDate = \Carbon\Carbon::parse($start);
                                    $endDate = \Carbon\Carbon::parse($end);
                                    $days = $startDate->diffInDays($endDate) + 1;

                                    return "$days hari";
                                }

                                return '-';
                            })
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan/Keterangan')
                            ->required()
                            ->rows(4)
                            ->columnSpan(2),

                        Forms\Components\FileUpload::make('attachment')
                            ->label('Lampiran Surat Keterangan')
                            ->directory('attendance-permissions/attachments')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(2048)
                            ->helperText('Upload surat keterangan dokter atau orang tua (PDF/Gambar, Max 2MB)')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Catatan Persetujuan')
                            ->rows(3)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->hidden(fn ($record) => ! $record),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                Tables\Columns\TextColumn::make('student.class.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sick' => 'warning',
                        'permission' => 'info',
                        'other' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sick' => 'Sakit',
                        'permission' => 'Izin',
                        'other' => 'Lainnya',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Durasi')
                    ->suffix(' hari')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('attachment')
                    ->label('Lampiran')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Disetujui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Siswa')
                    ->relationship('student', 'full_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Izin')
                    ->options([
                        'sick' => 'Sakit',
                        'permission' => 'Izin',
                        'other' => 'Lainnya',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('approval_notes')
                                ->label('Catatan')
                                ->rows(2),
                        ])
                        ->action(function (AttendancePermission $record, array $data) {
                            $record->approve(auth()->id(), $data['approval_notes'] ?? null);

                            Notification::make()
                                ->success()
                                ->title('Surat Izin Disetujui')
                                ->send();
                        })
                        ->hidden(fn (AttendancePermission $record) => $record->status !== 'pending'),

                    Tables\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('approval_notes')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->rows(2),
                        ])
                        ->action(function (AttendancePermission $record, array $data) {
                            $record->reject(auth()->id(), $data['approval_notes']);

                            Notification::make()
                                ->danger()
                                ->title('Surat Izin Ditolak')
                                ->send();
                        })
                        ->hidden(fn (AttendancePermission $record) => $record->status !== 'pending'),

                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Setujui Semua')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->approve(auth()->id());
                                }
                            });

                            Notification::make()
                                ->success()
                                ->title('Surat Izin Berhasil Disetujui')
                                ->send();
                        }),

                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAttendancePermissions::route('/'),
            'create' => Pages\CreateAttendancePermission::route('/create'),
            'edit' => Pages\EditAttendancePermission::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();

        return $pending > 0 ? 'warning' : 'success';
    }
}
