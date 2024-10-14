<?php

namespace App\Tons\Transactions;

use Carbon\Carbon;

class CollectTransactionDepositAttribute implements CollectAttributeInterface
{
    public function collect(array $data): array
    {
        return [
            'hash' => null,
            'lt' => null,
            'total_fees' => null,
            'to_memo' => null,
            'from_address_wallet' => null,
            'amount' => null,
            'type' => config('services.ton.deposit'),
            'currency' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
