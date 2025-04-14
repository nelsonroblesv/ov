<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CustomLogin;
use App\Filament\Resources\AsignarTipoSemanaResource\Widgets\SemanaActual;
use App\Filament\Resources\AsignarTipoSemanaResource\Widgets\SemanaActualWidget;
use App\Filament\Resources\AsignarTipoSemanaResource\Widgets\SemanaWidget;
use App\Filament\Resources\UbicacionUsuarioResource\Widgets\MapRecorridosWidget;
use App\Filament\Widgets\MapRecorridosWidget as WidgetsMapRecorridosWidget;
use App\Filament\Widgets\SemanaActual as WidgetsSemanaActual;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dashboard')
            ->path('dashboard')
            ->unsavedChangesAlerts()
            ->login(CustomLogin::class)
            ->profile()
            ->brandLogo(fn() => view('filament.logo'))
            ->darkModeBrandLogo(fn() => view('filament.dark-logo'))
            ->databaseNotifications()
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->font('Poppins')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                SemanaActualWidget::class,
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                WidgetsMapRecorridosWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Ver como Usuario')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-eye')
            ])
            ->navigationGroups([
                'Clientes & Prospectos',
                'Pedidos & Pagos',
                'Catalogo',
                'Bitacora',
                'Administrar'
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

    public function boot(): void
    {
        FilamentColor::register([
            'custom-black' => [
                'light' => 'bg-black text-white',
                'dark' => 'bg-gray-900 text-white',
            ],
            'custom_gray' => '#757275',
            'custom_light_blue' => '#018079'
        ]);
    }
}
