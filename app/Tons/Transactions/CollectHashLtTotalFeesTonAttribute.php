<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;

class CollectHashLtTotalFeesTonAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $trans = array(
            'hash' => TransactionHelper::toHash(Arr::get($data, 'hash')),
            'lt' => Arr::get($data, 'lt'),
            'total_fees' => (int)Arr::get($data, 'total_fees') / pow(10, TransactionHelper::TON_DECIMALS),
        );
        return array_merge($parentTrans, $trans);
    }
}
