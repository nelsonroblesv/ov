<?php

namespace App\Providers;

use App\Models\Pedido;
use Illuminate\Support\ServiceProvider;


use App\Models\PedidosItems;
use App\Models\PreferredModuleItem;
use App\Observers\PedidosItemsObserver;
use App\Observers\PreferredItemObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PedidosItems::observe(PedidosItemsObserver::class);
        PreferredModuleItem::observe(PreferredItemObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es']); // also accepts a closure
        });

        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/pedidos.css'),
        ]);
    }
}
