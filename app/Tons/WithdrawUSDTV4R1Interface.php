<?php

namespace App\Tons;

interface WithdrawUSDTV4R1Interface
{
    public function process(string $mnemo, string $destAddress, string $usdtAmount);
}
