<?php

namespace App\Tons\Transactions;

class CollectCurrencyTypeTonAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $trans = array(
            'type' => config('services.ton.deposit'),
            'currency' => config('services.ton.ton'),
        );
        return array_merge($parentTrans, $trans);
    }
}
