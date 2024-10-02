<?php

namespace App\Tons\Withdraws;

interface WithdrawTonV4R2Interface
{
    public function process(array $phrases, string $toAddress, string $tonAmount, string $comment = "");
}
