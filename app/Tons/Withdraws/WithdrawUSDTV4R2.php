<?php

namespace App\Tons\Withdraws;

use Olifanton\Ton\Contracts\Wallets\V4\WalletV4Options;
use Olifanton\Ton\Contracts\Wallets\V4\WalletV4R2;

class WithdrawUSDTV4R2 extends WithdrawUSDTAbstract implements WithdrawUSDTV4R2Interface
{
    public function getWallet($pubicKey): WalletV4R2
    {
        return new WalletV4R2(new WalletV4Options(publicKey: $pubicKey));
    }
}
