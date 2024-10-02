<?php

namespace App\Tons;

interface WithdrawTonV4R2Interface
{
    public function process(string $mnemo, string $toAddress, string $tonAmount);
}
