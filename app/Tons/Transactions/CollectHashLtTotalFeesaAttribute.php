<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;

class CollectHashLtTotalFeesaAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $trans = [
            'hash' => Arr::get($data, 'hash'),
            'lt' => Arr::get($data, 'lt'),
            'total_fees' => Arr::get($data, 'total_fees'),
        ];
        return array_merge($parentTrans, $trans);
    }
}
