<?php

namespace App\Tons\Withdraws;

interface WithdrawUSDTV4R2Interface
{
    public function process(string $mnemo, string $destAddress, string $usdtAmount, string $comment = "");
}
