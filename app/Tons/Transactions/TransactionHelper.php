<?php

namespace App\Tons\Transactions;

class TransactionHelper
{
    const TON_DECIMALS = 9;

    const USDT_DECIMALS = 9;

    public static function toHash(string $data): string
    {
        $ll = base64_decode($data);
        return bin2hex($ll);
    }

    public static function toToken(float $amount, string $currency): float|int
    {
        if (!in_array($currency, config('services.ton.valid_currencies'))) {
            return 0;
        }
        $decimals = null;
        if ($currency === config('services.ton.usdt')) {
            $decimals = self::USDT_DECIMALS;
        } elseif ($currency === config('services.ton.ton')) {
            $decimals = self::TON_DECIMALS;
        }
        $unit = floor($amount * $decimals);
        return $unit / $decimals;
    }

    /**
     * @throws \Exception
     */
    public static function uniqueTransactionHash(): string
    {
        $bytes = random_bytes(32);
        return bin2hex($bytes);
    }
}
