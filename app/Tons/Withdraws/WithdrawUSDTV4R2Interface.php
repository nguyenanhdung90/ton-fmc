<?php

namespace App\Tons\Withdraws;

interface WithdrawUSDTV4R2Interface
{
    public function process(array $phrases, string $destAddress, string $usdtAmount, string $comment = "");
}
