<?php

namespace App\Providers;

use App\Tons\DepositTon;
use App\Tons\DepositTonInterface;
use App\Tons\WithdrawTonV4R1;
use App\Tons\WithdrawTonV4R1Interface;
use App\Tons\WithdrawUSDTV4R2;
use App\Tons\WithdrawUSDTV4R2Interface;
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
            WithdrawTonV4R1Interface::class => WithdrawTonV4R1::class,
            WithdrawUSDTV4R2Interface::class => WithdrawUSDTV4R2::class,
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
