<?php

namespace App\Tons;

use Olifanton\Ton\Contracts\Wallets\V4\WalletV4Options;
use Olifanton\Ton\Contracts\Wallets\V4\WalletV4R2;


class WithdrawUSDTV4R1 extends WithdrawUSDTAbstract implements WithdrawUSDTV4R1Interface
{
    public function getWallet($pubicKey)
    {
        return new WalletV4R2(new WalletV4Options(publicKey: $pubicKey));
    }
}
