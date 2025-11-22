<?php

namespace App\Filament\Resources;

use App\Enums\StudentStatus;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classroom;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nickname')
                            ->label('Nama Panggilan')
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP Siswa')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Siswa')
                            ->email()
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('students/photos')
                            ->visibility('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Orang Tua/Wali')
                    ->schema([
                        Forms\Components\TextInput::make('parent_name')
                            ->label('Nama Orang Tua/Wali')
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('parent_phone')
                            ->label('No. HP Orang Tua')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('parent_email')
                            ->label('Email Orang Tua')
                            ->email()
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('parent_address')
                            ->label('Alamat Orang Tua')
                            ->rows(2)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Akademik')
                    ->schema([
                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('class', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $class = Classroom::find($state);
                                if ($class && $class->major_id) {
                                    $set('major_id', $class->major_id);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('major_id')
                            ->label('Jurusan')
                            ->relationship('major', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Tanggal Masuk')
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(StudentStatus::class)
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

                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

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
                    })
                    ->formatStateUsing(fn (string $state): string => $state),

                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('major.code')
                    ->label('Jurusan')
                    ->badge()
                    ->sortable()
                    ->placeholder('Belum ada jurusan'),

                Tables\Columns\TextColumn::make('parent_phone')
                    ->label('HP Orang Tua')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'graduated' => 'Lulus',
                        'transferred' => 'Pindah',
                        'dropped_out' => 'Keluar',
                    ])
                    ->default('active'),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
