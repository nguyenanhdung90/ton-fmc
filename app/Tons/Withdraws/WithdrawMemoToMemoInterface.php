<?php

namespace App\Tons\Withdraws;

interface WithdrawMemoToMemoInterface
{
    public function transfer(string $fromMemo, string $toMemo, float $amount, string $currency);
}
