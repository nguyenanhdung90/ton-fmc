<?php

namespace App\Tons\Deposits;

interface DepositTonInterface
{
    public function getBy(int $walletId): string;
}
