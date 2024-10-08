<?php

namespace App\Tons\Transactions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Olifanton\Interop\Address;

class CollectMemoSenderAmountAttribute extends CollectAttribute
{
    public function collect(array $data): array
    {
        $parentTrans = parent::collect($data);
        $decodedOpName = Arr::get($data, 'in_msg.decoded_op_name');
        $memo = $source = $amount = null;
        if ($decodedOpName === config('services.ton.jetton_notify')) {
            $memo = Arr::get($data, 'in_msg.decoded_body.forward_payload.value.value.text');
            $source = Arr::get($data, 'in_msg.decoded_body.sender');
            $amount = Arr::get($data, 'in_msg.decoded_body.amount');
        } else if ($decodedOpName === config('services.ton.text_comment')) {
            $memo = Arr::get($data, 'in_msg.decoded_body.text');
            $source = Arr::get($data, 'in_msg.source.address');
            $amount = Arr::get($data, 'in_msg.value');
        }
       $address = new Address($source);
        $trans = [
            'to_memo' => $memo,
            'from_address_wallet' => $address->toString(true, true, null, !config('services.tom.is_main')),
            'amount' => $amount,
        ];
        return array_merge($parentTrans, $trans);
    }
}
