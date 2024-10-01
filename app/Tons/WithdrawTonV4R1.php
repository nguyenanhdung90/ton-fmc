<?php

namespace App\Tons;

use Olifanton\Ton\Contracts\Wallets\V4\WalletV4Options;
use Olifanton\Ton\Contracts\Wallets\V4\WalletV4R2;


class WithdrawTonV4R1 extends WithdrawTonAbstract implements WithdrawTonV4R1Interface
{
    public function getWallet($pubicKey)
    {
        return new WalletV4R2(new WalletV4Options($pubicKey));
    }
}
