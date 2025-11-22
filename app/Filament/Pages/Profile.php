<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-m-user-circle';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $title = 'Profil';

    protected static string $view = 'filament.pages.profile';

    public User $user;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->fillForm();
    }

    protected function fillForm()
    {
        $data = [
            'name' => $this->user?->name,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'username' => $this->user?->username,
            'current_password' => null,
            'password' => null,
            'password_confirmation' => null,
        ];

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->inlineLabel()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('email')
                            ->inlineLabel()
                            ->required()
                            ->email()
                            ->rules([
                                Rule::unique('users', 'email')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('username')
                            ->inlineLabel()
                            ->required()
                            ->rules([
                                Rule::unique('users', 'username')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->inlineLabel()
                            ->rules([
                                Rule::unique('users', 'phone')->ignore($this->user?->id),
                            ])
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Old Password')
                            ->requiredWith('password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->columnSpanFull()
                            ->helperText('Leave empty if you dont wanna change password')
                            ->autocomplete('new-current-password'),
                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->requiredWith('current_password')
                            ->inlineLabel()
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->columnSpanFull()
                            ->autocomplete('new-password'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('New Password (Confirm)')
                            ->inlineLabel()
                            ->password()
                            ->columnSpanFull()
                            ->revealable()
                            ->autocomplete('new-password-confirmation'),
                    ]),
            ])
            ->statePath('data')
            ->model($this->user);
    }

    public function submit()
    {
        $input = $this->form->getState();

        $update = [
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'phone' => $input['phone'],
        ];

        if (filled($input['password'])) {
            if (Hash::check($input['current_password'], $this->user->password)) {
                $update['password'] = Hash::make($input['password']);
            } else {
                throw ValidationException::withMessages([
                    'data.current_password' => 'Your current password not match.',
                ]);
            }
        }

        $this->user->update($update);

        Notification::make()
            ->title('Profil berhasil diupdate')
            ->success()
            ->send();

        $this->fillForm();
    }
}
