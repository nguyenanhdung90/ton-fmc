<?php

namespace App\Tons;

interface WithdrawUSDTV4R2Interface
{
    public function process(string $mnemo, string $destAddress, string $usdtAmount);
}
