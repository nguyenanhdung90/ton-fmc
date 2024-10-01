<?php

namespace App\Providers;

use App\Tons\DepositTon;
use App\Tons\DepositTonInterface;
use App\Tons\WithdrawV4R1;
use App\Tons\WithdrawV4R1Interface;
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
        $appServices = [
            DepositTonInterface::class => DepositTon::class,
            WithdrawV4R1Interface::class => WithdrawV4R1::class,
        ];
        foreach ($appServices as $key => $value) {
            $this->app->bind($key, $value);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
