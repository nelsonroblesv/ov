<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Observers\PaymentObserver;
use App\Models\Payment;

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
    }
}
