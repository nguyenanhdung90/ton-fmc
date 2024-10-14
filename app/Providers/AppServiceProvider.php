<?php

namespace App\Providers;

use App\Tons\HttpClient\TonCenterV3Client;
use App\Tons\HttpClient\TonCenterV3ClientInterface;
use App\Tons\Withdraws\WithdrawTonV4R2;
use App\Tons\Withdraws\WithdrawTonV4R2Interface;
use App\Tons\Withdraws\WithdrawUSDTV4R2;
use App\Tons\Withdraws\WithdrawUSDTV4R2Interface;
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
            WithdrawTonV4R2Interface::class => WithdrawTonV4R2::class,
            WithdrawUSDTV4R2Interface::class => WithdrawUSDTV4R2::class,
            TonCenterV3ClientInterface::class => TonCenterV3Client::class,
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
