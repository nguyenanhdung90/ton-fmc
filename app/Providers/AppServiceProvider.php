<?php

namespace App\Providers;

use App\Tons\Deposits\DepositTon;
use App\Tons\Deposits\DepositTonInterface;
use App\Tons\Phrases\Phrase;
use App\Tons\Phrases\PhraseInterface;
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
            DepositTonInterface::class => DepositTon::class,
            WithdrawTonV4R2Interface::class => WithdrawTonV4R2::class,
            WithdrawUSDTV4R2Interface::class => WithdrawUSDTV4R2::class,
            PhraseInterface::class => Phrase::class,
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
