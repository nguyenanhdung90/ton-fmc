<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;

class CollectHashLtAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $trans = array(
            'hash' => TransactionHelper::toHash(Arr::get($data, 'hash')),
            'lt' => Arr::get($data, 'lt'),
        );
        return array_merge($parentTrans, $trans);
    }
}
