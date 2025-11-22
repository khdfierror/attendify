<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use App\Filament\Pages\Profile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\View\Components\Modal;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;

class AdminPanelProvider extends PanelProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Modal::closedByClickingAway(false);
        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(25)
                ->paginationPageOptions([5, 10, 25, 50, 100])
                ->emptyStateHeading('Tidak ada data');
        });
    }

    public function panel(Panel $panel): Panel
    {
        $listenModalLoading = [
            '__dispatch',
            'activeTab',
            'changeTab',
            'gotoPage',
            'nextPage',
            'previousPage',
            'sortTable',
            'tableRecordsPerPage',
            'removeTableFilter',
            'tableGrouping',
            'tableGroupingDirection',
            'toggledTableColumns',
            'tableSearch',
            'tableFilters',
            'resetTableSearch',
            'mountTableAction',
            'mountFormComponentAction',
            'mountedActionsData',
        ];

        return $panel
            ->default()
            ->id('admin')
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->url(fn () => Profile::getUrl()),
            ])
            ->navigationItems([

            ])
            ->navigationGroups([
                NavigationGroup::make('Data'),
                NavigationGroup::make('Akademik'),
                NavigationGroup::make('Pengaturan'),
            ])
            ->path('/')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Gray,
            ])
            ->spa()
            ->font('DM Sans')
            ->maxContentWidth('full')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook('panels::styles.before', fn (): string => Blade::render(<<<'HTML'
                <style>
                    /** Setting Base Font */
                    html, body{
                        font-size: 14px;
                    }
                </style>
            HTML))
            ->renderHook(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, fn (): View => view('components.total-records'))
            ->renderHook(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, fn (): string => Blade::render(<<<'HTML'
                <x-modal-loading wire:loading wire:target="{{ $target }}" />
            HTML, [
                'target' => implode(',', $listenModalLoading),
            ]))
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('
                    <x-wire-modal-loading />
                '),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->plugins([
                (new class extends \BezhanSalleh\FilamentShield\FilamentShieldPlugin
                {
                    public function register(Panel $panel): void
                    { /** dont register resource, just use custom role resource */
                    }
                })::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
