<?php

namespace App\Tons;

interface WithdrawTonV4R1Interface
{
    public function process(string $mnemo, string $toAddress, string $unit);
}
