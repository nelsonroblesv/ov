<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\ServiceProvider;

use App\Observers\PaymentObserver;
use App\Models\Payment;
use App\Models\PreferredModuleItem;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Observers\PreferredItemObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
        //
        Payment::observe(PaymentObserver::class);
        OrderItem::observe(OrderItemObserver::class);
        PreferredModuleItem::observe(PreferredItemObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es','en']); // also accepts a closure
        });
    }
}
