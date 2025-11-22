<?php

namespace App\Filament\Resources;

use App\Enums\TeacherStatus;
use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Guru';

    protected static ?string $modelLabel = 'Guru';

    protected static ?string $pluralModelLabel = 'Data Guru';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpan(2),

                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('teachers/photos')
                            ->visibility('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Kepegawaian')
                    ->schema([
                        Forms\Components\DatePicker::make('join_date')
                            ->label('Tanggal Bergabung')
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(TeacherStatus::class)
                            ->default('active')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'info',
                        'P' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('homeroomClasses.name')
                    ->label('Wali Kelas')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('join_date')
                    ->label('Bergabung')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
