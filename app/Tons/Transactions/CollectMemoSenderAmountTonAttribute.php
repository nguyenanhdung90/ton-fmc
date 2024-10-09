<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;
use Olifanton\Interop\Address;

class CollectMemoSenderAmountTonAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $memo = Arr::get($data, 'in_msg.message_content.decoded.comment');
        $source = Arr::get($data, 'in_msg.source');
        $address = new Address($source);
        $trans = [
            'to_memo' => $memo,
            'from_address_wallet' => $address->toString(true, true, null, !config('services.tom.is_main')),
            'amount' => Arr::get($data, 'in_msg.value') / pow(10, TransactionHelper::TON_DECIMALS),
        ];
        return array_merge($parentTrans, $trans);
    }
}
