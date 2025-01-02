<?php

namespace App\Providers\Filament;

use App\Filament\Auth\CustomLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->login(CustomLogin::class)
            ->profile()
            ->brandLogo(fn() => view('filament.logo'))
            ->databaseNotifications()
            ->favicon(asset('images/favicon.png'))
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
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Ver como Usuario')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-eye'),

                NavigationItem::make('Ciudad del Carmen')
                    ->group('Mapas & CP')
                    ->url('https://www.google.com/maps/d/viewer?mid=1hleTCi4flnguULTyPT1Ea0JT5DHMUhrM&femb=1&ll=18.65061144033998%2C-91.79841757725212&z=14')
                    ->icon('heroicon-o-map')
                    ->openUrlInNewTab(),

                NavigationItem::make('Campeche')
                    ->group('Mapas & CP')
                    ->url('https://www.google.com/maps/d/viewer?mid=1hleTCi4flnguULTyPT1Ea0JT5DHMUhrM&femb=1&ll=19.82777661056459%2C-90.52806061271251&z=13')
                    ->icon('heroicon-o-map')
                    ->openUrlInNewTab(),
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
