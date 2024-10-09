<?php

namespace App\Tons\Transactions;

class TransactionHelper
{
    const TON_DECIMALS = 9;

    const USDT_DECIMALS = 6;

    public static function toHash(string $data): string
    {
        $ll = base64_decode($data);
        return bin2hex($ll);
    }
}
