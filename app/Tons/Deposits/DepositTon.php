<?php

namespace App\Tons\Deposits;

use App\Models\TonWallet;

class DepositTon implements DepositTonInterface
{
    public function getBy(int $walletId): string
    {
        $tonWallet = TonWallet::find($walletId);
        return $tonWallet ? $tonWallet->address : "";
    }
}
