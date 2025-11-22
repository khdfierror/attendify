<?php

namespace App\Filament\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as Component;
use Illuminate\Validation\ValidationException;

/**
 * @property ComponentContainer $form
 */
class Login extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public $username = '';

    public $password = '';

    public $tahun = '';

    public $remember = false;

    public $useTahun = false;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if ($this->useTahun) {
            $this->form->fill([
                'tahun' => date('Y'),
            ]);
        }

    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(100_000);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'username' => $data['username'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'username' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        if ($this->useTahun) {
            session()->put('tahun', $data['tahun']);
        }

        return app(LoginResponse::class);
    }

    protected function getFormSchema(): array
    {
        $yearStart = 2023;

        return [
            TextInput::make('username')
                ->label('Username')
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(),
            Select::make('tahun')
                ->options(collect(range($yearStart, date('Y')))->mapWithKeys(fn ($year) => [$year => $year]))
                ->required()
                ->hidden(! $this->useTahun),
            Checkbox::make('remember')
                ->label('Ingat Saya'),
        ];
    }
}
