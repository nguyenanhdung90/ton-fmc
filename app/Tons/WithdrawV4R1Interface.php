<?php

namespace App\Tons;

interface WithdrawV4R1Interface
{
    public function process(string $mnemo, string $toAddress, string $unit);
}
