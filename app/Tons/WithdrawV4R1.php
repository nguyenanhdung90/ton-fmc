<?php

namespace App\Tons;

use Olifanton\Ton\Contracts\Wallets\V4\WalletV4Options;
use Olifanton\Ton\Contracts\Wallets\V4\WalletV4R2;


class WithdrawV4R1 extends WithdrawAbstract implements WithdrawV4R1Interface
{
    public function getWallet($pubicKey)
    {
        return new WalletV4R2(new WalletV4Options($pubicKey));
    }
}
