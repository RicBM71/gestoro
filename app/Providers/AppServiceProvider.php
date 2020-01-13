<?php

namespace App\Providers;

use Schema;
use App\Caja;
use App\Cobro;
use App\Compra;
use App\Albalin;
use App\Albaran;
use App\Comline;
use App\Deposito;
use App\Observers\CajaObserver;
use App\Observers\CobroObserver;
use App\Observers\CompraObserver;
use App\Observers\AlbalinObserver;
use App\Observers\AlbaranObserver;
use App\Observers\ComlineObserver;
use App\Observers\DepositoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Deposito::observe(DepositoObserver::class);
        Caja::observe(CajaObserver::class);
        Comline::observe(ComlineObserver::class);
        Albalin::observe(AlbalinObserver::class);
        Cobro::observe(CobroObserver::class);
        Compra::observe(CompraObserver::class);
        Albaran::observe(AlbaranObserver::class);

    }
}
