<?php

namespace App\Tons;

abstract class WithdrawUSDTAbstract
{
    abstract public function getWallet($pubicKey);

    public function process()
    {
        return 123;
    }
}
