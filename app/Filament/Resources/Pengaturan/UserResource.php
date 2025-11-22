<?php

namespace App\Filament\Resources\Pengaturan;

use App\Filament\Resources\Pengaturan\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = '/pengaturan/users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->inlineLabel()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('email')
                            ->inlineLabel()
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('username')
                            ->inlineLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->inlineLabel()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Password & Roles')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->nullable(fn (?string $operation) => $operation === 'edit')
                            ->required(fn (?string $operation) => $operation === 'create')
                            ->dehydrateStateUsing(static function (?string $state, string $operation) {
                                if ($operation === 'create') {
                                    return ! empty($state) ? Hash::make($state) : null;
                                } elseif ($state) {
                                    return Hash::needsRehash($state) ? Hash::make($state) : $state;
                                }
                            })
                            ->columnSpanFull()
                            ->helperText('Leave empty if you dont wanna change password')
                            ->autocomplete('new-current-password')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->tooltip('Generate Password')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Set $set) {
                                        $set('password', Str::random(6));
                                    })
                            ),
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->preload()
                            ->inlineLabel()
                            ->relationship('roles', 'label')
                            ->columnSpanFull(),
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.label')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('M j, Y')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Impersonate::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
